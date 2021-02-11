import React from "react";
import './Form.scss';

const Form = ({id, children, action, disabled, onInput, onSubmit}) => {
    return (
        <form id={id} className="Form" action={action} acceptCharset="UTF-8" onSubmit={(event) => onSubmit(event)}>
            {React.Children.map(children, child => (
                <div className="Form__input-wrapper p-2 d-flex">
                    {React.cloneElement(child, {
                        ...child.props,
                        className: `Form__${child.props.type === 'submit' ? 'submit' : 'input'} d-block p-2 w-100 ${child.props.className ? child.props.className : ''}`,
                        disabled,
                        onInput
                    })}
                </div>
            ))}
        </form>
    )
};

export default Form