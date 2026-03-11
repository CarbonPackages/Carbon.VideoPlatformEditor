import React from 'react';
import style from './style.module.css';
import {translate} from '@neos-project/neos-ui-i18n';
import {IVideo} from "../../domain";

export function MetadataView(props: {video: IVideo}) {
    return (
        <details className={style.details}>
            <summary>{translate('x:x:x', 'Video metadata')}</summary>

            <dl className={style.infoView}>
                <dt className={style.propertyLabel}>{translate('x:x:x', 'Title')}</dt>
                <dd className={style.propertyValue}>{props.video.title}</dd>

                <dt className={style.propertyLabel}>{translate('x:x:x', 'Aspect ratio')}</dt>
                <dd className={style.propertyValue}>{props.video.aspectRatio}</dd>


                <dt className={style.propertyLabel}>{translate('x:x:x', 'Platform type')}</dt>
                <dd className={style.propertyValue}>{props.video.platformType}</dd>

                <dt className={style.propertyLabel}>{translate('x:x:x', 'Video id')}</dt>
                <dd className={style.propertyValue}>{JSON.stringify(props.video.id)}</dd>

                <dt className={style.propertyLabel}>{translate('x:x:x', 'Video uri')}</dt>
                <dd className={style.propertyValue}>{props.video.uri}</dd>
            </dl>
        </details>
    );
}

const convertSeconds = (duration) => {
    const twoDigits = (number) => `0${number}`.slice(-2);
    const hours = ~~(duration / 3600);
    const minutes = ~~((duration % 3600) / 60);
    const seconds = duration % 60;

    if (hours) {
        return `${hours}:${twoDigits(minutes)}:${twoDigits(seconds)}`;
    }
    if (minutes) {
        return `${minutes}:${twoDigits(seconds)}`;
    }
    return seconds;
};
