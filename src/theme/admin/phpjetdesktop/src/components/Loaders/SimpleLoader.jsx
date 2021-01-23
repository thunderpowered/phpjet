import React from "react";

const SimpleLoader = () => {
    return (
        <div className={'SimpleLoader text-center p-2 position-absolute'}>
            <div className="lds-dual-ring"/>
        </div>
    )
};

export default SimpleLoader