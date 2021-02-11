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
        this.urlSetMode = globalSystemRootURL + globalSystemActions['setPanelMode'];
        this.urlGetMode = globalSystemRootURL + globalSystemActions['getPanelMode'];
        this.urlSetDefaultWindow = globalSystemRootURL + globalSystemActions['setDefaultWindow'];
        this.urlGetDefaultWindow = globalSystemRootURL + globalSystemActions['getDefaultWindow'];
        // i added some explanation because things became a little more complicated than i thought it would be, so it is just to keep in mind
        // i do not overuse comments!
        this.state = {
            // classic or window or maybe something more will be added in the future
            panelMode: 'default',
            // in classic mode we have to load some window to fill empty space
            defaultWindow: 0,
            // is start menu opened
            showStartMenu: false,
            // array with window configs
            windowConfig: [],
            // array with actual React components
            windowComponents: [],
            // represents id of last opened/clicked window (for startBar in classic mode)
            windowOnTop: -1,
            mousePosition: {top: 0, left: 0},
            // whether context menu opened or not
            contextMenu: false,
            // by default we hide workspace and show only when everything is loaded
            // it'd be good to add some loading animation or something, but i got a lot to do, will think about it later
            opacity: 0
        };
    }

    componentDidMount() {
        document.addEventListener('mousedown', () => (
            // if click anywhere -> close context menu
            this.setState(() => ({contextMenu: false}))
        ));
    }

    // load config array from Windows component
    loadMenu(windowConfig) {
        this.setState(() => ({'windowConfig': windowConfig}), () => {
            return this.loadPanelMode();
        });
    }

    // Toggles menu visibility
    onClickStart(show = false) {
        this.setState(() => ({showStartMenu: show}))
    }

    // this is temporary function, i'll find better solution later
    tempLoadChildWindow(index = [], windowData = {}, reloadWindow = true) {
        let newWindow = this.state.windowConfig[index[0]];
        for (let i = 1; i < index.length; i++) {
            newWindow = newWindow.children[index[i]];
        }

        if (typeof newWindow === 'undefined') {
            console.log('window is undefined');
            return false;
        }

        newWindow.parentIndex = index.length - 2;
        index = this.state.windowConfig.length;
        this.setState(() => (
            {windowConfig: [...this.state.windowConfig, newWindow]}
            // proceed as usual
        ), () => {
            this.onClickMenu(index, windowData, reloadWindow);
            this.setState(() => (
                // and delete it from config
                {windowConfig: this.state.windowConfig.filter((window, _index) => index !== _index)}
            ))
        });
    }

    // If menu item is chosen -> create new window
    onClickMenu(index, windowData = {}, reloadWindow = false) {
        // if index doesn't exist
        if (typeof this.state.windowConfig[index] === 'undefined') {
            return false;
        }

        // if window already created
        let label = this.state.windowConfig[index].label;
        let indexExisting = this.state.windowComponents.findIndex(window => (
            window.props.title === label
        ));

        if (indexExisting > -1) {
            // window is already exists
            if (reloadWindow) {
                // destroy existing (and then create new ofc)
                this.destroyWindow(indexExisting, index);
            } else {
                // just push it on top
                return this.onSortWindows(indexExisting);
            }
        }

        // Prepare component, Window component is wrapper that should wrap each Window
        let newIndex = this.state.windowComponents.length;
        let windowComponent = <Window
            // do not change key value! it should be set only once and should be unique
            key={newIndex}
            visible={true}
            configIndex={index}
            title={this.state.windowConfig[index]['label']}
            children={this.state.windowConfig[index].component}
            parent={this.state.windowConfig[index].parentIndex}
            windowData={windowData}
            onSortWindows={this.onSortWindows.bind(this)}
            onDestroy={this.destroyWindow.bind(this)}
            onMinifyWindow={this.onMinifyWindow.bind(this)}
        />;

        this.setState(() => (
            {
                windowComponents: [...this.state.windowComponents, windowComponent],
                windowOnTop: index
            }
        ));
    }

    // Each window has its own order, while we click on it, it should bubble to the top
    // So when we click, we should sort em
    onSortWindows(index) {
        // take current component
        let currentComponent = this.state.windowComponents[index];
        // delete it from array and then again push it on top
        let newWindowComponents = [...this.state.windowComponents.filter((component, _index) => index !== _index), currentComponent];

        this.setState(() => ({
            windowComponents: newWindowComponents,
            windowOnTop: currentComponent.props.configIndex
        }));
    }

    // delete window
    destroyWindow(index, configIndex, event) {
        if (typeof event !== 'undefined') {
            event.stopPropagation();
        }
        if (typeof this.state.windowComponents[index] !== 'undefined') {
            this.setState(() => ({
                windowComponents: this.state.windowComponents.filter((component, _index) => index !== _index)
            }));
        }
    }

    onMinifyWindow(index) {
        let newWindowComponents = this.state.windowComponents.map((component, _index) => {
            // it sucks that each time i have to change something i have to clone this element
            return index === _index ? React.cloneElement(component, {...component.props, visible: !component.props.visible}) : component;
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
                        // after panel state loaded, let's show the workspace!
                        // short delay is for reinsurance
                        setTimeout(() => {
                            this.setState(() => ({opacity: 1}));
                        }, 100);
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
                        if (!this.state.windowComponents.length) {
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
                        if (!this.state.windowComponents.length) {
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
        return <div id={'Workspace'}
                    style={{opacity: this.state.opacity}}
                    className={`w-100 h-100 position-relative Desktop__Workspace--${this.state.panelMode.charAt(0).toUpperCase() + this.state.panelMode.slice(1)}Mode`}
                    onClick={() => this.onClickStart(false)}
                    onContextMenu={(e) => this.onContextMenu(e)}>

            {/* We don't need background in classic mode, so let's jst not render it */}
            {this.state.panelMode === 'window' &&
                <Background/>
            }

            <Windows onMount={this.loadMenu.bind(this)}
                     onLoadChildWindow={this.tempLoadChildWindow.bind(this)}>
                {/* here are windows */}
                {this.state.windowComponents.map((child, _index) => (
                    React.cloneElement(child, {
                        ...child.props,
                        index: _index
                    })
                ))}
            </Windows>

            <StartMenu panelMode={this.state.panelMode}
                       windowConfig={this.state.windowConfig}
                       windowOnTop={this.state.windowOnTop}
                       showStartMenu={this.state.showStartMenu}
                       onClickLogout={this.props.onClickLogout}
                       onClickMenu={this.onClickMenu.bind(this)}
                       onClickStart={this.onClickStart.bind(this)}
                       onContextMenu={this.setDefaultWindow.bind(this)}
            />

            <TaskBar showStartMenu={this.state.showStartMenu}
                     windows={this.state.windowComponents}
                     onMinifyWindow={this.onMinifyWindow.bind(this)}
                     onClickStart={this.onClickStart.bind(this)}
            />

            <SimpleDropMenu active={this.state.contextMenu}
                            mouse={this.state.mousePosition}
                            hoverClass={'theme__background-color--hover'}>
                <div onMouseDown={(e) => e.stopPropagation()}>
                    {/* It is possible to add more modes in the future */}
                    {this.state.panelMode !== 'classic' &&
                    <div className={'w-100 h-100 p-4 pt-2 pb-2 d-block theme__cursor-pointer'}
                         onClick={() => this.setPanelMode('classic')}>
                        Switch to Classic Mode
                    </div>
                    }
                    {this.state.panelMode !== 'window' &&
                    <div className={'w-100 h-100 p-4 pt-2 pb-2 d-block theme__cursor-pointer'}
                         onClick={() => this.setPanelMode('window')}>
                        Switch to Window Mode
                    </div>
                    }
                </div>
            </SimpleDropMenu>

        </div>
    }
}