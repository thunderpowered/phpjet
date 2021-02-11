import React, {Component} from 'react';

export class EngineVersion extends Component {
    constructor() {
        super();
        this.state = {engineVersion: globalDesktopMisc.engineVersion};
    }

    render() {
        return <span className={'Desktop__Elements__Info--EngineVersion text-center d-block fs-8'}>{this.state.engineVersion}</span>
    }
}