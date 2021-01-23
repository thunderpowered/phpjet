import React from "react";
import SimpleLoader from './components/loaders/SimpleLoader';
import Workspace from './layouts/Workspace';
import Auth from './layouts/Auth';
import {createStore} from 'redux';
import {Provider} from 'react-redux';

const store = createStore();

export default class Desktop extends React.Component {
    render() {
        if (typeof this.props.authorized === 'undefined') {
            return <SimpleLoader/>
        }

        return (
            <Provider store={store}>
                {this.props.authorized
                    ? <Workspace/>
                    : <Auth/>
                }
            </Provider>
        )
    }
}