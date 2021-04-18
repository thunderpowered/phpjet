import React from "react";
import Tasklist from "./Tasklist";
import StartButton from "../Buttons/StartButton";
import './Tarkbar.scss';
import ClocksContainer from "../Clocks/ClocksContainer";
import Language from "../Widgets/Language";

const Taskbar = ({windows, onClickStartButton, onClickTask}) => {
    return (
        <div className={'taskbar p-0 position-fixed fixed-bottom w-100 d-flex flex-row flex-nowrap justify-content-start align-items-stretch'}>
            <StartButton onClick={onClickStartButton}/>
            <Tasklist onClick={onClickTask} windows={windows}/>
            <div className="taskbar__widget-rack justify-self-end ml-auto">
                <Language />
                {/*<ClocksContainer/>*/}
            </div>
        </div>
    )
};

export default Taskbar