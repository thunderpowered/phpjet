import React, {Component} from 'react';
import {Background} from "./blocks/Background";

export class Workspace extends Component {
    render() {
        return <div id={'Workspace'}>
            <Background/>
            Workspace
        </div>
    }
}