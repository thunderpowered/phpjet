import React, {Component} from 'react';
import {Background} from "./blocks/Background";
import {TaskBar} from "./blocks/TaskBar";
import {Windows} from "./blocks/Windows";
import {StartMenu} from "./blocks/StartMenu";
import {Window} from "../elements/windows/Window";
import {SimpleDropMenu} from "../elements/dropdowns/SimpleDropMenu";
import {fetch2} from "../../../helpers/fetch2";

export class Workspace extends Component {
    constructor() {
        super();
        this.urlSetMode = '/admin/misc/setMode';
        this.urlGetMode = '/admin/misc/getMode';
        this.urlSetDefaultWindow = '/admin/misc/setDefaultWindow';
        this.urlGetDefaultWindow = '/admin/misc/getDefaultWindow';
        // i think this is too much, maybe i can find a solution to simplify this?
        this.state = {
            panelMode: 'default',
            defaultWindow: 0,
            showStartMenu: false,
            windowConfig: [],
            windowComponents: [],
            windowOrder: [],
            windowOnTop: -1,
            mousePosition: {top: 0, left: 0},
            contextMenu: false,
            opacity:0
        };
    }

    componentDidMount() {
        document.addEventListener('mousedown', () => (
            this.setState(() => ({contextMenu: false}))
        ));
        setTimeout(() => {
            this.setState(() => ({opacity:1}));
        }, 1000);
    }

    loadMenu(windowConfig) {
        this.setState(() => ({'windowConfig': windowConfig}), () => {
            this.loadPanelMode();
        });
    }

    // Toggles menu visibility
    onClickStart(show = false) {
        this.setState(() => ({showStartMenu: show}))
    }

    // If menu item is chosen -> create new window
    onClickMenu(index) {
        // if index doesn't exist
        if (typeof this.state.windowConfig[index] === 'undefined') {
            return false;
        }

        // if window already created
        if (this.state.windowOrder.indexOf(index) > -1) {
            // just push it on top
            return this.onSortWindows(index);
        }

        // Prepare component, Window component is wrapper that should wrap each Window
        let newIndex = this.state.windowComponents.length;
        let windowComponent = {
            active: true,
            index: newIndex,
            zIndex: newIndex,
            configIndex: index,
            onSortWindows: this.onSortWindows.bind(this),
            onDestroy: this.destroyWindow.bind(this),
            title: this.state.windowConfig[index]['label'],
            component: this.state.windowConfig[index].component
        };

        this.setState(() => (
            {
                windowComponents: [...this.state.windowComponents, windowComponent],
                windowOrder: [...this.state.windowOrder, index],
                windowOnTop: index
            }
        ));
    }

    // Each window has its own order, while we click on it, it should bubble to the top
    // So when we click, we should sort em
    onSortWindows(configIndex) {
        let newWindowOrder = [...this.state.windowOrder.filter((item) => item !== configIndex), configIndex];
        let newWindowComponents = this.state.windowComponents.map((item) => {
            return Object.keys(item).length ? {...item, zIndex: newWindowOrder.indexOf(item.configIndex)} : {};
        });
        this.setState(() => ({windowOrder: newWindowOrder, windowComponents: newWindowComponents, windowOnTop: configIndex}));
    }

    destroyWindow(index, configIndex, event) {
        event.stopPropagation();
        if (typeof this.state.windowComponents[index] !== 'undefined') {
            this.setState(() => ({
                // i know it looks like crutch, but i do really need it here
                // because React doesn't render the components entirely, it just swap em or something
                // for instance, if we have two components: 0 and 1, if i delete 0, 1 gets state of 0
                // so to prevent this -> don't delete component from array, just set it to undefined. it won't render and won't mess with states
                windowComponents: this.state.windowComponents.map((item, _index) => index === _index ? {} : item),
                // windowComponents: this.state.windowComponents.filter((item, _index) => index !== _index),
                windowOrder: this.state.windowOrder.filter((item) => item !== configIndex)
            }));
        }
    }

    onClickWindows(index) {
        let newWindowComponents = this.state.windowComponents.map((item, _index) => {
            return Object.keys(item).length && _index === index ? {...item, active: !item.active} : item;
        });
        this.setState(() => ({windowComponents: newWindowComponents}));
    }

    onContextMenu(e) {
        e.preventDefault();
        e.stopPropagation();
        this.setState(() => ({mousePosition: {top: e.clientY, left: e.clientX}, contextMenu: true}));
    }

    loadPanelMode() {
        return fetch2(this.urlGetMode, {}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined' && typeof result.data.panelMode !== 'undefined') {
                    this.setState(() => ({panelMode: result.data.panelMode}), () => {
                        if (this.state.panelMode === 'classic') {
                            this.loadDefaultWindow();
                        }
                    });
                }
            }
        });
    }

    setPanelMode(panelMode) {
        return fetch2(this.urlSetMode, {queryParams: {'panelMode': panelMode}}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined' && typeof result.data.panelMode !== 'undefined') {
                    this.setState(() => ({panelMode: result.data.panelMode}), () => {
                        if (!this.state.windowOrder.length) {
                            this.onClickMenu(this.state.defaultWindow);
                        }
                    });
                    this.setState(() => ({contextMenu: false}));
                }
            }
        });
    }

    loadDefaultWindow() {
        return fetch2(this.urlGetDefaultWindow, {}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined' && typeof result.data.defaultWindow !== 'undefined') {
                    this.setState(() => ({defaultWindow: result.data.defaultWindow}), () => {
                        if (!this.state.windowOrder.length) {
                            this.onClickMenu(this.state.defaultWindow);
                        }
                    });
                }
            }
        });
    }

    setDefaultWindow(event, index) {
        return fetch2(this.urlSetDefaultWindow, {queryParams: {'defaultWindow': index}}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined' && typeof result.data.defaultWindow !== 'undefined') {
                    this.setState(() => ({defaultWindow: result.data.defaultWindow}));
                }
            }
        });
    }

    render() {
        return <div style={{opacity: this.state.opacity}} className={`w-100 h-100 position-relative Desktop__Workspace--${this.state.panelMode.charAt(0).toUpperCase() + this.state.panelMode.slice(1)}Mode`} id={'Workspace'}
                    onClick={() => this.onClickStart(false)} onContextMenu={(e) => this.onContextMenu(e)}>
            {this.state.panelMode === 'window' &&
                <Background/>
            }
            <Windows windowOrder={this.windowOrder}
                     onMount={this.loadMenu.bind(this)}>
                {this.state.windowComponents.map((item, index) => {
                    if (!Object.keys(item).length) return;
                    return <Window active={item.active}
                                   index={index}
                                   zIndex={item.zIndex}
                                   title={item.title}
                                   configIndex={item.configIndex}
                                   onSortWindows={item.onSortWindows}
                                   onDestroy={item.onDestroy}
                                   onClickWindows={this.onClickWindows.bind(this)}>
                        {item.component}
                    </Window>
                })}
            </Windows>
            <StartMenu windowConfig={this.state.windowConfig}
                       windowOnTop={this.state.windowOnTop}
                       showStartMenu={this.state.showStartMenu}
                       onClickLogout={this.props.onClickLogout}
                       onClickMenu={this.onClickMenu.bind(this)}
                       onClickStart={this.onClickStart.bind(this)}
                       onContextMenu={this.setDefaultWindow.bind(this)}
            />
            <TaskBar showStartMenu={this.state.showStartMenu} onClickStart={this.onClickStart.bind(this)}
                     windows={this.state.windowComponents} onClickWindows={this.onClickWindows.bind(this)}/>

            <SimpleDropMenu active={this.state.contextMenu} mouse={this.state.mousePosition} hoverClass={'theme__background-color--hover'}>
                <div onMouseDown={(e) => e.stopPropagation()}>
                    {this.state.panelMode !== 'classic' &&
                        <div className={'w-100 h-100 p-4 pt-2 pb-2 d-block theme__cursor-pointer'} onClick={() => this.setPanelMode('classic')}>
                            Switch to Classic Mode
                        </div>
                    }
                    {this.state.panelMode !== 'window' &&
                        <div className={'w-100 h-100 p-4 pt-2 pb-2 d-block theme__cursor-pointer'} onClick={() => this.setPanelMode('window')}>
                            Switch to Window Mode
                        </div>
                    }
                </div>
            </SimpleDropMenu>
        </div>
    }
}