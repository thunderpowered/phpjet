import React, {Component} from 'react';
import {TaskListItem} from "./TaskList/TaskListItem";

export class TaskList extends Component {
    render() {
        return <div className={'p-0 pt-0 pb-0 d-flex flex-nowrap justify-content-start align-items-center'} id={'TaskList'}>
            {this.props.windows.map((item) => {
                if (!Object.keys(item).length) return;
                return <TaskListItem onMinifyWindow={this.props.onMinifyWindow} task={item} />
            })}
        </div>
    }
}