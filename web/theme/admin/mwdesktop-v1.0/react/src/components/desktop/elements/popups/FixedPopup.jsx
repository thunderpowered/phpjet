import React, {Component} from 'react';

export class FixedPopup extends Component {

    constructor() {
        super();
        this.state = {height: 500};
        // ref is reference, i.e. 'link'
        // this.innerRef = React.createRef();
    }

    render() {
        return <div
            className={'w-100 h-100 position-absolute Desktop__Elements__Popups--FixedPopup-background theme__background-opacity'}>
            <div className={'container'}>
                <div className={'row vh-100'}>
                    <div
                        className={'col-lg-5 col-md-9 col-sm-12 m-auto fixed-center Desktop__Elements__Popups--FixedPopup-window theme__background-color2 theme__fixed-absolute--center'}>
                        <div className={'p-3 Desktop__Elements__Popups--FixedPopup-inner'}>
                            {this.props.children}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}