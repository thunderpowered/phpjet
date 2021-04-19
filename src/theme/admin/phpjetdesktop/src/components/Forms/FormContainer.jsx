import React from "react";
import {connect} from 'react-redux';
import Form from "./Form";
import {createForm, setDisabledStatus, setInputValue} from "../../actions/forms";
import {sendForm} from "../../api/forms";

class FormContainer extends React.Component {
    constructor(props) {
        super(props);
    }

    componentDidMount() {
        const {dispatch, id} = this.props;
        dispatch(createForm(id));
    }

    onInput(event) {
        const {dispatch, id} = this.props;
        dispatch(setInputValue(id, event.target.name, event.target.value));
    }

    onSubmit(event) {
        event.preventDefault();
        const {dispatch, values, action, id, onSubmit} = this.props;
        dispatch(sendForm(action, values, id, onSubmit), result => onSubmit(result));
    }

    render() {
        const {disabled, action, id, children} = this.props;
        return (
            <Form
                id={id}
                action={action}
                disabled={disabled}
                onSubmit={this.onSubmit.bind(this)}
                onInput={this.onInput.bind(this)}>
                {children}
            </Form>
        )
    }
}

const mapStateToProps = (state, props) => typeof state.forms[props.id] !== 'undefined' ? state.forms[props.id] : {};

export default connect(mapStateToProps)(FormContainer)