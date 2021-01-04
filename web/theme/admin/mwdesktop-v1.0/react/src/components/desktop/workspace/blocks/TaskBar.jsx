import React, {Component} from 'react';
import {Start} from "./TaskBar/Start";
import {TaskList} from "./TaskBar/TaskList";
import {Clocks} from "../../elements/widgets/Clocks";

export class TaskBar extends Component {

    render() {
        return <div
            className={'Desktop__Workspace__Blocks--TaskBar p-3 pt-0 pb-0 position-fixed fixed-bottom w-100 theme__background-color2 d-flex flex-row flex-nowrap justify-content-start align-items-stretch'}
            id={'TaskBar'}>
            <Start showStartMenu={this.props.showStartMenu} onClickStart={this.props.onClickStart}/>
            <TaskList/>
            <div className={'Desktop__Workspace__Blocks--TaskBar__widget-rack justify-self-end ml-auto'}>
                <Clocks/>
            </div>
        </div>
    }
}