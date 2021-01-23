import React from 'react';
import ReactDOM from 'react-dom';
import Desktop from "./Desktop";
import {createStore, applyMiddleware} from 'redux';
import {Provider} from 'react-redux';
import reducer from './reducers';
import thunk from 'redux-thunk';

const store = createStore(reducer, applyMiddleware(thunk));
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