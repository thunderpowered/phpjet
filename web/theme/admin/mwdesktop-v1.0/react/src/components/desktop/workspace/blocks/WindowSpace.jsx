import React, {Component} from 'react';
import {StartMenu} from "./WindowSpace/StartMenu";

export class WindowSpace extends Component {
    constructor() {
        super();
    }

    render() {
        // WindowSpace is just container that shouldn't be visible, i need it just to pack all windows in some container
        return <div className={'Desktop__Workspace__Blocks--WindowSpace vh-100 position-absolute overflow-hidden theme__background-transparent'} id={'WindowSpace'}>
            <StartMenu onClickLogout={this.props.onClickLogout} showStartMenu={this.props.showStartMenu}/>
            {this.props.children}
        </div>
    }
}