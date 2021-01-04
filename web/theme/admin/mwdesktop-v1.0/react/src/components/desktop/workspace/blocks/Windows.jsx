import React, {Component} from 'react';
// import windows
import {WindowAdminActions} from "./Windows/WindowAdminActions";


export class Windows extends Component {
    constructor(props) {
        super(props);
        this.state = {windows: {}};
        this.windowsConfig = [
            {
                'component': <WindowAdminActions/>,
                'windowName': 'WindowAdminActions',
                'label': 'Recent activity',
                // font awesome https://fontawesome.com/icons/
                'icon': 'fa-user-shield'
            }
        ];
    }

    componentDidMount() {
        this.props.onMount(this.windowsConfig);
        // as we pass it to level above, we don't need it here
        // actually i think it'd be greater to store window config above
        // todo fine workaround
        this.windowsConfig = [];
    }


    render() {
        // React does not allow using objects, so just convert object to array
        let windows = Object.values(this.props.windows);
        return <div
            className={'Desktop__Workspace__Blocks--WindowSpace vh-100 position-absolute overflow-hidden theme__background-transparent'}
            id={'Windows'}>
            {windows}
        </div>
    }
}