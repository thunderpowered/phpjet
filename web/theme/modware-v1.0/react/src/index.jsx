import React from 'react';
import ReactDOM from 'react-dom';
import {Banner} from './components/banner/Banner';
import {Search} from "./components/search/Search";

// Banner Component
const bannerContainer = document.getElementById("Banner");
if (bannerContainer) {
    ReactDOM.render(
        <Banner/>,
        document.getElementById('Banner')
    );
}

// Search Component
ReactDOM.render(
    <Search/>,
    document.getElementById('Search')
);