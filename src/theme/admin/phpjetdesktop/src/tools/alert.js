const messageVisibilityDuration = 5000;

export const shoutOut = (message, style = '') => {
    // todo include this plugin so it can be compiled along with the rest of the project
    if (typeof Msg !== 'undefined' && typeof Msg[style] === 'function') {
        Msg[style](message, messageVisibilityDuration);
    } else {
        alert(message);
    }
};