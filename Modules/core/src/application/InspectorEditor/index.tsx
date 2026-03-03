import React from 'react';
import {Label, TextInput, Button, IconButton, Icon} from '@neos-project/react-ui-components';
import {translate} from '@neos-project/neos-ui-i18n';
import {getVideo} from "../../infrastructure/http";

export const createInspectorEditor = (deps: {ImageEditor: React.Component}) => {
    const {ImageEditor} = deps;

    return function InspectorEditor(props) {
        const setTitle = React.useCallback((title) => {
            if (props.value !== null && typeof props.value === 'object') {
                props.commit({
                    ...props.value,
                    title
                });
            }
        }, [props.commit, props.value]);

        const setImage = React.useCallback((imageObject, _saveHooks) => {
            // _saveHooks are ignored as cropping is currently not supported
            if (props.value !== null && typeof props.value === 'object') {
                props.commit({
                    ...props.value,
                    poster: imageObject ? {
                        id: imageObject.__identity
                    } : null
                });
            }
        }, [props.commit, props.value]);

        const imageObject = React.useMemo(() => {
            if (typeof props.value === 'object' && props.value?.poster?.id) {
                return {
                    __identity: props.value.poster.id
                };
            }
            return null;
        }, [props.value]);

        const [searchVideoId, setSearchVideoId] = React.useState();

        const [isLoading, setIsLoading] = React.useState();

        const searchVideo = React.useCallback(async (videoId) => {
            setIsLoading(true);
            let video = null;
            try {
                video = await getVideo({
                    videoId,
                });
            } finally {
                setIsLoading(false);
            }
            if ("success" in video) {
                props.commit({
                    id: videoId,
                    title: video.success.videoTitle,
                    poster: {
                        id: video.success.posterImageId
                    }
                });
            }
        }, []);

        if (isLoading) {
            return <Icon icon="spinner" spin size="2x" />
        }

        if (typeof props.value !== 'object' || !props.value) {
            return <>
                <Label htmlFor="carbon-VideoPlatformEditor-video-id">{translate('Carbon.VideoPlatformEditor:Main:inspector.select', 'Select video')}</Label>
                <TextInput
                    id="carbon-VideoPlatformEditor-video-id"
                    value={searchVideoId ?? ''}
                    onChange={setSearchVideoId}
                />
                <Button onClick={() => searchVideo(searchVideoId)}>Search</Button>
            </>
        }

        return <>
            <Label htmlFor="carbon-VideoPlatformEditor-video-id">{translate('Carbon.VideoPlatformEditor:Main:inspector.video', 'Video')}: "{props.value.id}"</Label>
            <IconButton icon="sync" onClick={() => searchVideo(props.value.id)} />
            <IconButton icon="xmark" onClick={() => props.commit("")} />

            <Label htmlFor="carbon-VideoPlatformEditor-title">{translate('Carbon.VideoPlatformEditor:Main:inspector.title', 'Title')}</Label>
            <TextInput
                id="carbon-VideoPlatformEditor-title"
                value={props.value?.title ?? ''}
                onChange={setTitle}
            />
            <Label htmlFor="carbon-VideoPlatformEditor-poster">{translate('Carbon.VideoPlatformEditor:Main:inspector.poster', 'Poster')}</Label>
            <ImageEditor
                id="carbon-VideoPlatformEditor-poster"
                options={{
                    features: {
                        crop: false
                    }
                }}
                value={imageObject}
                commit={setImage}
                renderSecondaryInspector={props.renderSecondaryInspector}
            />
        </>
    }
}
