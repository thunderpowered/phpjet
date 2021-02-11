import React, {Component} from 'react';

export class SimpleLoader extends Component {
    render() {
        return <div className={'Desktop__Elements__Loaders--SimpleLoader text-center p-2 position-absolute'}>
            <div className="lds-dual-ring"/>
        </div>
    }
}