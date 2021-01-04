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
    alert('Unable to start the application. Reload page and try again.');
}