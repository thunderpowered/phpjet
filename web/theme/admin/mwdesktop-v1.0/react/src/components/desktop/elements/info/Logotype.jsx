import React, {Component} from 'react';
import {fetch2} from "../../../../helpers/fetch2";

export class Logotype extends Component {
    constructor() {
        super();
        this.state = {backgroundImage: ''};

        this.urlGetLogotype = '/admin/media/getLogotype';
        this.loadBackgroundImage();
    }

    setLogotype(logotype) {
        this.setState(() => {
            return {backgroundImage: logotype}
        });
    }

    loadBackgroundImage() {
        return fetch2(this.urlGetLogotype, {}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined' && typeof result.data.logotype !== 'undefined') {
                    this.setLogotype(result.data.logotype);
                }
            }
        });
    }

    render() {
        return <div style={{'backgroundImage': `url('${this.state.backgroundImage}')`}}
                    className={'p-3 Desktop__Elements__Info--Logotype theme__background-image theme__background-image--contain'}/>
    }
}