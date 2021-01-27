import React from "react";
import {connect} from 'react-redux';
import Form from "./Form";

class FormContainer extends React.Component {
    constructor(props) {
        super(props);
    }

    onInput(event) {

    }

    onSubmit(event) {
        event.preventDefault();
    }

    render() {
        const {disabled, action, id, children} = this.props;
        return (
            <Form id={id} action={action} disabled={disabled} onSubmit={this.onSubmit.bind(this)} onInput={this.onInput.bind(this)}>
                {children}
            </Form>
        )
    }
}

const mapStateToProps = (state, props) => ({
    disabled: typeof state.forms[props.id] === 'undefined' ? false : state.forms[props.id].disabled
});

export default connect(mapStateToProps)(FormContainer)