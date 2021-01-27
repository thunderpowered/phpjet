export const OPEN_CONTEXT_MENU = 'OPEN_CONTEXT_MENU';
export const CLOSE_CONTEXT_MENU = 'CLOSE_CONTEXT_MENU';

export const openContextMenu = (children, mousePosition) => ({
    type: OPEN_CONTEXT_MENU,
    children,
    mousePosition
});

export const closeContextMenu = () => ({
    type: CLOSE_CONTEXT_MENU
});