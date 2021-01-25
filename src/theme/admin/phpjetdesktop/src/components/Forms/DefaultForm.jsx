import './DefaultForm.scss';

const DefaultForm = ({id, children, action, disabled, onInput, onSubmit}) => {
    return (
        <form id={id} className="DefaultForm" action={action} acceptCharset="UTF-8" onSubmit={(event) => onSubmit(event)}>
            {React.Children.map(children, child => (
                <div className="DefaultForm__input-wrapper p-2 d-flex">
                    {React.cloneElement(child, {
                        ...child.props,
                        className: `DefaultForm__input d-block p-2 w-100 ${child.props.className ? child.props.className : ''}`,
                        disabled,
                        onInput
                    })}
                </div>
            ))}
        </form>
    )
};

export default DefaultForm