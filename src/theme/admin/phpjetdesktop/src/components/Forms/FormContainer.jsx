import React from "react";
import {connect} from 'react-redux';

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
        const {type, action, id, children} = this.props;
        return <type id={id} action={action} disabled={this.state.disabled} onSubmit={this.onSubmit.bind(this)} onInput={this.onInput.bind(this)}>
            {children}
        </type>
    }
}

const mapStateToProps = (state, props) => ({
    disabled: state.forms[props.id].disabled
});

export default connect(mapStateToProps)(FormContainer)