import React from 'react';
import ReactDOM from 'react-dom';
import {Desktop} from "./components/desktop/Desktop";

// Root component
const desktopContainer = document.getElementById('Desktop');
if (desktopContainer) {
    ReactDOM.render(
        <Desktop/>,
        desktopContainer
    );
} else {
    console.log('Unable to create Root component.');
}