import {SET_MISC, SET_TIME} from "../actions/misc";

const initialState = {
    logotype: '',
    version: '',
    currentTime: {
        time: '',
        date: ''
    },
    serverTimeOffset: 0, // relative to UTC in seconds,
    serverTimeZone: '',
    serverTimeUTC: 0
};

const misc = (state = initialState, action) => {
    switch (action.type) {
        case SET_MISC:
            return {
                ...state,
                logotype: action.misc.logotype,
                version: action.misc.version,
                serverTimeOffset: action.misc.serverTimeOffset,
                serverTimeZone: action.misc.serverTimeZone,
                serverTimeUTC: action.misc.serverTimeUTC
            };

        case SET_TIME:
            return {
                ...state,
                currentTime: {
                    time: action.time.time,
                    date: action.time.date
                }
            };

        default:
            return state;
    }
};

export default misc