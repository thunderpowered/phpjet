import React, {Component} from 'react';

export class TaskListItem extends Component {
    render() {
        return <div onClick={() => {
            this.props.onMinifyWindow(this.props.index)
        }}
                    className={'user-select-none p-3 Desktop__Workspace__Blocks__TaskBar__TaskList--TaskListItem theme__background-color3 theme__background-color--hover-soft theme__border-left theme__border--thicker theme__border-color text-left' + (this.props.active ? ' Desktop__Workspace__Blocks__TaskBar__TaskList--TaskListItem--active' : '')}>
            {this.props.title}
        </div>
    }
}