import React, {Component} from 'react';
// import windows
import {WindowAdminActions} from "./Windows/WindowAdminActions";
import {WindowPageBuilder} from "./Windows/WindowPageBuilder";


export class Windows extends Component {
    constructor(props) {
        super(props);
        this.windowsConfig = [
            {
                'component': <WindowAdminActions/>,
                'windowName': 'WindowAdminActions',
                'label': 'Recent activity',
                // font awesome https://fontawesome.com/icons/
                'icon': 'fa-user-shield'
            },
            {
                'component': <WindowPageBuilder/>,
                'windowName': 'WindowPageBuilder',
                'label': 'Page Builder',
                'icon': 'fa-magic'
            }
        ];
    }

    componentDidMount() {
        this.props.onMount(this.windowsConfig);
        delete this.windowsConfig;
    }


    render() {
        return <div
            className={'Desktop__Workspace__Blocks--Windows vh-100 position-absolute overflow-hidden theme__background-transparent'}
            id={'Windows'}>
            {this.props.children}
        </div>
    }
}