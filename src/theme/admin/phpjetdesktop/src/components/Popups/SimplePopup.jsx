import React from "react";
import './SimplePopup.scss'

const SimplePopup = ({children}) => {
    return (
        <div className="popup-simple w-100 h-100 position-absolute">
            <div className="container">
                <div className="row">
                    <div className="popup-simple__content-wrapper col-xxl-3 col-xl-6 col-lg-9 col-md-12 m-auto fixed-center">
                        <div className="popup-simple__content">
                            {children}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
};

export default SimplePopup