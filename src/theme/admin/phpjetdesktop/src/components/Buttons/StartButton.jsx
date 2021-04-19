import React from "react";
import './StartButton.scss';

const StartButton = ({onClick}) => (
    <div onClick={onClick} className={'button-start p-4 pt-2 pb-2 m-0'}>
        <div
            className="button-start__icon w-100 h-100 d-flex flex-row flex-nowrap justify-content-center align-items-center">
            <i className="fab fa-old-republic d-block"/>
        </div>
    </div>
);

export default StartButton