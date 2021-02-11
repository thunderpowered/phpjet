import React, {Component} from 'react';
// import windows
import {WindowAdminActions} from "./Windows/WindowAdminActions";
import {WindowAllPages} from "./Windows/WindowAllPages";
import {WindowPageBuilder_v1} from "./Windows/WindowAllPages/WindowPageBuilder_v1";


export class Windows extends Component {
    constructor(props) {
        super(props);
        // order is like in menu -> top elements are on top
        this.windowsConfig = [
            {
                'component': <WindowAllPages/>,
                'windowName': 'WindowAllPages',
                'label': 'Pages',
                'icon': 'fa-columns',
                'children': [
                    {
                        'component': <WindowPageBuilder_v1/>,
                        'windowName': 'WindowPageBuilder',
                        'label': 'Page Builder',
                        'icon': 'fa-magic'
                    }
                ]
            },
            {
                'component': <WindowAdminActions/>,
                'windowName': 'WindowAdminActions',
                'label': 'Recent activity',
                // font awesome https://fontawesome.com/icons/
                'icon': 'fa-user-shield',
                'children': []
            }
        ];
    }

    componentDidMount() {
        this.props.onMount(this.windowsConfig);
        delete this.windowsConfig;
    }

    openChildWindow(childIndex, parentIndex, windowData, reloadExisting = true) {
        this.props.onLoadChildWindow([parentIndex, childIndex], windowData, reloadExisting);
    }

    render() {
        return <div
            className={'Desktop__Workspace__Blocks--Windows vh-100 position-absolute overflow-hidden theme__background-transparent'}
            id={'Windows'}>
            {this.props.children.map(child => (
                React.cloneElement(child, {
                    ...child.props,
                    openChildWindow: (childIndex, windowData) => this.openChildWindow(childIndex, child.props.configIndex, windowData)
                }))
            )}
        </div>
    }
}