import React from "react";
import {useTranslation} from "react-i18next";
import './Clocks.scss';

const Clocks = ({time, date, timeZone}) => {
    const {t} = useTranslation('common');
    return (
        <div title={`${t('Time.ServerTime')}${timeZone ? ` (${timeZone})` : ''}`} className={'Clocks p-3 m-1 mt-0 mb-0 h-100 user-select-none'}>
            <b>{time}</b> {date}
        </div>
    )
};

export default Clocks