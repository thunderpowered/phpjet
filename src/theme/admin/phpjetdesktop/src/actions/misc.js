export const SET_MISC = 'SET_MISC';
export const SET_TIME = 'SET_TIME';

export const setMisc = misc => ({
    type: SET_MISC,
    misc
});

export const setTime = time => ({
    type: SET_TIME,
    time
});