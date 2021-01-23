import React from "react";

export const SimpleDropdown = ({active, mousePosition, children}) => {
    return (
        <div className={'SimpleDropdown position-fixed'}
             style={{
                 'top': mousePosition.top,
                 'left': mousePosition.left,
                 'display': active ? 'block' : 'none'
             }}>
            {React.Children.map(children, (child, index) => (
                <div className={'w-100 SimpleDropdown__wrapper d-flex'}>
                    {React.cloneElement(child, {
                        ...child.props,
                        className: (child.props.className ? child.props.className : '') + ` w-100 text-left d-block SimpleDropdown__item theme__background-color2 theme__text-color theme__border-bottom theme__border-color`,
                    })}
                </div>
            ))}
        </div>
    );
}