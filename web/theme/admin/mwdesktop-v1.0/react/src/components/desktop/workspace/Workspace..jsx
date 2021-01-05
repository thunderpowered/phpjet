import React, {Component} from 'react';
import {Background} from "./blocks/Background";
import {TaskBar} from "./blocks/TaskBar";
import {Windows} from "./blocks/Windows";
import {StartMenu} from "./blocks/StartMenu";
import {Window} from "../elements/windows/Window";

export class Workspace extends Component {
    constructor() {
        super();
        // i think this is too much, maybe i can find a solution to simplify this?
        this.state = {showStartMenu: false, windowConfig: [], windowComponents: [], windowOrder: []};
    }

    loadMenu(windowConfig) {
        this.setState(() => ({'windowConfig': windowConfig}));
    }

    // Toggles menu visibility
    onClickStart(show = false) {
        this.setState(() => ({showStartMenu: show}))
    }

    // If menu item is chosen -> create new window
    onClickMenu(index) {
        if (this.state.windowOrder.indexOf(index) > -1 || typeof this.state.windowConfig[index] === 'undefined') {
            return false;
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
                windowOrder: [...this.state.windowOrder, index]
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
        this.setState(() => ({windowOrder: newWindowOrder, windowComponents: newWindowComponents}));
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

    render() {
        return <div id={'Workspace'}>
            <Background/>
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
                       showStartMenu={this.state.showStartMenu}
                       onClickLogout={this.props.onClickLogout}
                       onClickMenu={this.onClickMenu.bind(this)}
                       onClickStart={this.onClickStart.bind(this)}
            />
            <TaskBar showStartMenu={this.state.showStartMenu} onClickStart={this.onClickStart.bind(this)}
                     windows={this.state.windowComponents} onClickWindows={this.onClickWindows.bind(this)}/>
        </div>
    }
}