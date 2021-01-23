import React, {Component} from 'react';
import {fetch2} from "../../../../helpers/fetch2";

export class BasicForm extends Component {
    constructor() {
        super();
        this.action = '';
        this.input = {};
        this.state = {disabled: false};
    }

    onSubmit(event) {
        event.preventDefault();

        if (!Object.keys(this.input).length) {
            return false;
        }

        let formValid = this.validateForm();
        if (!formValid) {
            return false;
        }

        this.blockForm();
        return fetch2(this.action, {
            queryParams: this.input
        }, {
            onSuccess: (result) => {
                this.unblockForm();
                if (typeof result.status === 'undefined' || !result.status) {
                    return this.showErrors();
                }
                if (typeof result.action !== 'undefined' && typeof this.props.actions !== 'undefined' && typeof this.props.actions[result.action] !== 'undefined') {
                    this.props.actions[result.action](result.data);
                }
            },
            onError: (error) => {
                this.unblockForm();
            }
        });
    }

    validateForm() {
        // todo
        return true;
    }

    blockForm() {
        this.setState(() => ({disabled: true}));
    }

    unblockForm() {
        this.setState(() => ({disabled: false}));
    }

    onInput(event) {
        if (typeof event.target === 'undefined') {
            return false;
        }

        if (typeof this.input[event.target.name] === 'undefined') {
            this.input[event.target.name] = '';
        }
        this.input[event.target.name] = event.target.value;
    }

    showErrors() {
        // todo
    }

    render() {
        this.action = this.props.action;
        // we assume that .children is just list of inputs and nothing more
        return <form className={'Desktop__Elements__Forms--BasicForm theme__background-color2'} action={this.action}
                     acceptCharset={"UTF-8"} onSubmit={this.onSubmit.bind(this)}>
            {
                React.Children.map(this.props.children, child => (
                    <div className={'p-2 Desktop__Elements__Forms--BasicForm-input-wrapper d-flex'}>
                        {React.cloneElement(child, {
                            ...child.props,
                            className: (child.props.className ? child.props.className : '') + ' d-block p-2 theme__background-color3 theme__text-color',
                            style: {...child.props.style, width: '100%'},
                            onInput: this.onInput.bind(this),
                            disabled: this.state.disabled
                        })}
                    </div>
                ))
            }
        </form>
    }
}