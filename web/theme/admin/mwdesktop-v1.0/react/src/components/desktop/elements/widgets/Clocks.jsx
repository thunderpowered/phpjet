import React, {Component} from 'react';
import {fetch2} from "../../../../helpers/fetch2";

export class Clocks extends Component {
    constructor() {
        super();
        this.state = {time: '00:00:00', date: '00.00.0000', timeZone: ''};
        this.serverTimeCorrection = 0;
        this.urlGetServerTime = globalSystemRootURL + globalSystemActions['getTime'];
        this.loadServerTime();
    }

    loadServerTime() {
        return fetch2(this.urlGetServerTime, {}, {
            onSuccess: (result) => {
                if (typeof result.data !== 'undefined' && typeof result.data.serverTimeUTC !== 'undefined' && typeof result.data.serverTimeOffset !== 'undefined') {
                    this.startClocks(result.data.serverTimeOffset * 1000, result.data.serverTimeUTC * 1000);
                    this.setState(() => ({timeZone: result.data.serverTimeZone}));
                }
            }
        });
    }

    startClocks(serverTimeOffset, serverTimeUTC) {
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
        this.setState(() => ({time: fullTime, date: fullDate}));

        this.timeout = setTimeout(() => {
            this.startClocks(serverTimeOffset, serverTimeUTC + 1000)
        }, 1000);
    }

    render() {
        return <div
            className={'Desktop__Elements__Widgets--Clocks p-3 m-1 mt-0 mb-0 h-100 theme__background-color3 theme__border-right theme__border-color theme__border--thicker user-select-none'}
            title={'Server Time' + (this.state.timeZone ? ` (${this.state.timeZone})` : '')} id={'Clocks'}>
            <b>{this.state.time}</b> {this.state.date}
        </div>
    }
}