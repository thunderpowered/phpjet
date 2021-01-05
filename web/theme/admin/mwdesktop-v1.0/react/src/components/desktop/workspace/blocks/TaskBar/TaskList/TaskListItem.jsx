import React, {Component} from 'react';

export class TaskListItem extends Component {
    render() {
        // I'm so fucking tired of writing this long class names
        // Maybe it wasn't good idea? :(
        return <div onClick={() => {
            this.props.onClickWindows(this.props.task.index)
        }}
                    className={'user-select-none p-3 Desktop__Workspace__Blocks__TaskBar__TaskList--TaskListItem theme__background-color3 theme__background-color--hover-soft theme__border-left theme__border--thicker theme__border-color text-left' + (this.props.task.active ? ' Desktop__Workspace__Blocks__TaskBar__TaskList--TaskListItem--active' : '')}>
            {this.props.task.title}
        </div>
    }
}