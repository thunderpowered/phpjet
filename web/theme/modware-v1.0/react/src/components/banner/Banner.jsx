import React, {Component} from 'react';
export class Banner extends Component {
    /**
     *
     * @type {string}
     */
    imageURL;

    constructor() {
        super();
        this.loadImageURL();
    }

    loadImageURL() {
        let bannerElement = document.getElementById('Banner');
        this.imageURL = bannerElement.getAttribute('data-background');
    }

    render() {
        return <div style={{backgroundImage: `url(${this.imageURL})`}} className={"header__banner-image"}></div>
    }
}