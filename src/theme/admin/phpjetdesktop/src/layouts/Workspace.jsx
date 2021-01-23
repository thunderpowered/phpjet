import React from 'react';
import {PANEL_MODE_WINDOW} from "../constants/mode";
import {BackgroundContainer} from "../components/Background/BackgroundContainer";

export class Workspace extends React.Component {
    render() {
        return (
            <div className="Workspace">
                {this.props.panelMode === PANEL_MODE_WINDOW &&
                    <BackgroundContainer/>
                }
            </div>
        )
    }
}