import React, {Component} from 'react';
import {TaskListItem} from "./TaskList/TaskListItem";

export class TaskList extends Component {
    render() {
        // since task bar must display element in order they were opened (i'll add ability to change sorting in task bar in the future), and since array always change the order, we have to sort it before displaying
        // fortunately, we have property "key" that represents exactly order of adding
        // and yeah, we have to create copy to prevent mutating of original array (since all js arrays passed by link)
        let windows = [...this.props.windows].map((window, _index) => (
            {...window, index: _index}
        )).sort((component1, component2) => (
            +component1.key - +component2.key
        ));

        console.log(windows);

        return <div className={'p-0 pt-0 pb-0 d-flex flex-nowrap justify-content-start align-items-center'}
                    id={'TaskList'}>
            {windows.map(window => (
                <TaskListItem onMinifyWindow={this.props.onMinifyWindow} title={window.props.title}
                              index={window.index} active={window.props.active}/>
            ))}
        </div>
    }
}