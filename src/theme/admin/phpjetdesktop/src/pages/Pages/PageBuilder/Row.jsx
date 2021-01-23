import React from "react";

export default Row = ({htmlClass, children}) => {
    return (
        <div className="row">
            <div className={htmlClass}>
                {children}
            </div>
        </div>
    )
}