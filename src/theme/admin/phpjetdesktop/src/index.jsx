import React from 'react';
import ReactDOM from 'react-dom';
import {Desktop} from "./components/desktop/Desktop";
import {createStore} from 'redux';
import {Provider} from 'react-redux';
import reducer from './reducers';

const store = createStore(reducer);
const desktopElement = document.getElementById('Desktop');
if (desktopElement) {
    ReactDOM.render(
        <Provider store={store}>
            <Desktop/>
        </Provider>,
        desktopElement
    );
} else {
    alert('Unable to start the application. Reload page and try again.');
}