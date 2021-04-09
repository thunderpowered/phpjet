const messageVisibilityDuration = 5000;

export const shoutOut = (message, style = '') => {
    console.log(typeof Msg);
    if (typeof Msg[style] === 'function') {
        Msg[style](message, messageVisibilityDuration);
    } else {
        alert(message);
    }
};