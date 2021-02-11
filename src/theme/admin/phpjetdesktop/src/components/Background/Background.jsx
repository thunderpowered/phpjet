import React from 'react';
import './Background.scss';

const Background = ({wallpaper, onContextMenu}) => {
    return (
        <div style={{backgroundImage: `url(${wallpaper})`}}
             className={'Background vh-100 w-100 position-absolute overflow-hidden'}
             onContextMenu={onContextMenu}
        />
    )
};

export default Background