import React, {Component} from 'react';

export class Logotype extends Component {
    constructor() {
        super();
        this.state = {backgroundImage: globalDesktopMisc.logotype};
    }

    render() {
        return <div style={{'backgroundImage': `url('${this.state.backgroundImage}')`}}
                    className={'p-3 Desktop__Elements__Info--Logotype theme__background-image theme__background-image--contain'}/>
    }
}