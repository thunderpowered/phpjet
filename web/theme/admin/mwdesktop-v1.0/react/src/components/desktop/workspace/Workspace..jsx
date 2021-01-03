import React, {Component} from 'react';
import {Background} from "./blocks/Background";
import {TaskBar} from "./blocks/TaskBar";
import {WindowSpace} from "./blocks/WindowSpace";

export class Workspace extends Component {
    constructor() {
        super();
        this.state = {showStartMenu: false}
    }

    onClickStart() {
        // if "start button" clicked
        this.setState(() => ({showStartMenu: !this.state.showStartMenu}))
    }

    render() {
        return <div id={'Workspace'}>
            <Background/>
            <WindowSpace onClickLogout={this.props.onClickLogout} showStartMenu={this.state.showStartMenu}/>
            <TaskBar onClickStart={(e) => this.onClickStart(e)}/>
        </div>
    }
}