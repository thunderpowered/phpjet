import React from "react";
import {connect} from 'react-redux';
import Clocks from "./Clocks";
import {setTime} from "../../actions/misc";

class ClocksContainer extends React.Component {
    componentDidMount() {
        const {serverTimeUTC, serverTimeOffset} = this.props;
        this.calculateTime(serverTimeOffset * 1000, serverTimeUTC * 1000);
    }

    calculateTime(serverTimeOffset, serverTimeUTC) { // todo adjust algo, current doesn't work right (for some obvious reasons)
        const {dispatch} = this.props;

        let dateObject = new Date();
        if (!this.serverTimeCorrection) {
            this.serverTimeCorrection = dateObject.getTime() - serverTimeUTC;
        }

        // Adjust timezone, because clocks widget should display server local time, not my local time
        let myTimeZoneOffset = dateObject.getTimezoneOffset() * 60000;
        dateObject = new Date(dateObject.getTime() - this.serverTimeCorrection + (myTimeZoneOffset + serverTimeOffset));

        let date = dateObject.getDate();
        let year = dateObject.getFullYear();
        let hours = dateObject.getHours();
        let minutes = dateObject.getMinutes();
        let seconds = dateObject.getSeconds();

        let month = dateObject.toLocaleString('en-US', {month: 'short'});

        hours = hours < 10 ? "0" + hours : hours;
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        date = date < 10 ? "0" + date : date;

        let fullTime = `${hours}:${minutes}:${seconds}`;
        let fullDate = `${date} ${month} ${year}`;

        dispatch(setTime({time: fullTime, date: fullDate}));
        this.timeout = setTimeout(() => {
            this.calculateTime(serverTimeOffset, serverTimeUTC + 1000) // THAT's not very good, since we rely on js event loop which is not precise enough to calculate time
        }, 1000);
    }

    render() {
        const {currentTime, serverTimeZone} = this.props;
        return <Clocks time={currentTime.time} date={currentTime.date} timeZone={serverTimeZone}/>
    }
}

const mapStateToProps = state => ({
    serverTimeOffset: state.misc.serverTimeOffset,
    serverTimeZone: state.misc.serverTimeZone,
    serverTimeUTC: state.misc.serverTimeUTC,
    currentTime: state.misc.currentTime,
});

export default connect(mapStateToProps)(ClocksContainer)
