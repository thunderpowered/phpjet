import React, {Component} from 'react';
import {fetch2} from "../../../../helpers/fetch2";

export class EngineVersion extends Component {
    constructor() {
        super();
        this.state = {engineVersion: ''};
        this.urlEngineVersion = '/admin/info/engineVersion';
        this.loadEngineVersion();
    }

    setEngineVersion(engineVersion) {
        this.setState(() => {
            return {engineVersion: engineVersion}
        });
    }

    loadEngineVersion() {
        return fetch2(this.urlEngineVersion, {}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined' && typeof result.data.engineVersion !== 'undefined') {
                    this.setEngineVersion(result.data.engineVersion);
                }
            }
        });
    }

    render() {
        return <span className={'Desktop__Elements__Info--EngineVersion text-center d-block fs-8'}>{this.state.engineVersion}</span>
    }
}