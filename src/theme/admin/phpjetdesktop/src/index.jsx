import React from 'react';
import ReactDOM from 'react-dom';
import Desktop from "./Desktop";
import {createStore, applyMiddleware} from 'redux';
import {Provider} from 'react-redux';
import reducer from './reducers';
import thunk from 'redux-thunk';
import {I18nextProvider} from "react-i18next";
import i18next from "i18next";
import common_en from "./translations/en/common.json";

i18next.init({
    interpolation: { escapeValue: false },
    lng: 'en',
    resources: {
        en: {
            common: common_en // todo add other languages
        }
    }
});

const store = createStore(reducer, applyMiddleware(thunk));
const desktopElement = document.getElementById('Desktop');
if (desktopElement) {
    ReactDOM.render(
        <Provider store={store}>
            <I18nextProvider i18n={i18next}>
                <Desktop/>
            </I18nextProvider>
        </Provider>,
        desktopElement
    );
} else {
    alert('Unable to start the application. Reload the page and try again.');
}

window.onerror = (message, file, line, col, error) => {
    Msg.error("Unexpected runtime error occurred. Please press F5 to restart the application.", 5000);
    // setTimeout(() => window.location.reload(), 5000);
    console.error(message);
};