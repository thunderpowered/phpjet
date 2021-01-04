import React, {Component} from 'react';
import {Background} from "./blocks/Background";
import {TaskBar} from "./blocks/TaskBar";
import {Windows} from "./blocks/Windows";
import {StartMenu} from "./blocks/StartMenu";
import {Window} from "../elements/windows/Window";

export class Workspace extends Component {
    constructor() {
        super();
        // windowConfig - initial array (see Windows component)
        // windows -> actual rendered windows
        this.state = {showStartMenu: false, windowConfig: [], windows: {}};
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
        if (typeof this.state.windows[index] !== 'undefined' || typeof this.state.windowConfig[index] === 'undefined') {
            return false;
        }

        // Prepare component, Window component is wrapper that should wrap each Window
        let windowComponent = <Window onDestroy={() => {
            this.destroyWindow(index)
        }} title={this.state.windowConfig[index]['label']}>
            {this.state.windowConfig[index].component}
        </Window>;

        this.setState(() => (
            {
                windows: {...this.state.windows, [index]: windowComponent}
            }
        ));
    }

    destroyWindow(index) {
        if (typeof this.state.windows[index] !== 'undefined') {
            this.setState(() => ({windows: Object.assign({}, this.state.windows, {[index]: undefined})}));
        }
    }

    render() {
        return <div id={'Workspace'}>
            <Background/>
            <Windows windows={this.state.windows} onMount={this.loadMenu.bind(this)}/>
            <StartMenu windowConfig={this.state.windowConfig}
                       showStartMenu={this.state.showStartMenu}
                       onClickMenu={this.onClickMenu.bind(this)}
                       onClickLogout={this.props.onClickLogout}
                       onClickStart={this.onClickStart.bind(this)}
            />
            <TaskBar showStartMenu={this.state.showStartMenu} onClickStart={this.onClickStart.bind(this)}/>
        </div>
    }
}