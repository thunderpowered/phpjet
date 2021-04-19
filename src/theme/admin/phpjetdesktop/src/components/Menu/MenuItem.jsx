import React from "react";
import './MenuItem.scss'

const MenuItem = ({active, icon, label, onClick}) => {
    return (
        <div onClick={onClick} className={`menu-item p-4 pt-3 pb-3 fs-5 fw-light d-flex align-items-center ${active ? 'menu-item--active' : ''}`}>
            <div className="menu-item__icon-container fa-container m-1">
                <i className={`d-inline-block fs-5 fas ${icon}`}/>
            </div>
            <span className={'menu-item__text d-inline-block fs-6 mx-3'}>{label}</span>
        </div>
    )
}

export default MenuItem