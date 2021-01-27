import React from "react";
import './ContextMenu.scss';

const ContextMenu = ({children, mousePosition, opened = false}) => (
    <div className={'ContextMenu position-fixed'}
         style={{
             'top': mousePosition.y,
             'left': mousePosition.x,
             'display': opened ? 'block' : 'none'
         }}>
        {React.Children.map(children, child => (
            <div className={'ContextMenu__item-wrapper d-flex'}>
                {React.cloneElement(child, {
                    ...child.props,
                    className: `${child.className ? child.className : ''} p-4 pt-2 pb-2 w-100 text-left d-block ContextMenu__item`,
                    onClick: (event) => {event.stopPropagation(); child.props.onClick(event)}
                })}
            </div>
        ))}
    </div>
);

export default ContextMenu