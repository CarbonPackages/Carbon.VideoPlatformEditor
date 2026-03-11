import React from 'react';
import {Label, TextInput, Button, IconButton, Icon} from '@neos-project/react-ui-components';
import {translate} from '@neos-project/neos-ui-i18n';
import {getVideo} from "../../infrastructure/http";
import {IVideo} from "../../domain";
import {MetadataView} from "../MetadataView";
import style from './style.module.css';

export const createInspectorEditor = (deps: {ImageEditor: React.Component}) => {
    const {ImageEditor} = deps;

    return function InspectorEditor(props: {value: IVideo | null, commit: (video: IVideo | '') => void, renderSecondaryInspector: (identifier?: string, component?: any) => void}) {
        const setImage = React.useCallback((imageObject, _saveHooks) => {
            // _saveHooks are ignored as cropping is currently not supported
            if (props.value !== null && typeof props.value === 'object') {
                props.commit({
                    ...props.value,
                    thumbnail: imageObject ? {
                        id: imageObject.__identity
                    } : null
                });
            }
        }, [props.commit, props.value]);

        const imageObject = React.useMemo(() => {
            if (typeof props.value === 'object' && props.value?.thumbnail?.id) {
                return {
                    __identity: props.value.thumbnail.id
                };
            }
            return null;
        }, [props.value]);

        const [searchVideoId, setSearchVideoId] = React.useState();

        const [isLoading, setIsLoading] = React.useState();

        const searchVideo = React.useCallback(async (videoUri: string) => {
            if (!videoUri) {
                return;
            }
            setIsLoading(true);
            let videoResponse = null;
            try {
                videoResponse = await getVideo({
                    videoUri,
                });
            } finally {
                setIsLoading(false);
            }
            if (videoResponse && "success" in videoResponse) {
                props.commit(videoResponse.success);
            }
        }, []);

        if (isLoading) {
            return <div className={style.loader}>
                <Icon icon="spinner" spin size="2x" />
            </div>
        }

        if (typeof props.value !== 'object' || !props.value) {
            return <div className={style.searchBar}>
                <TextInput
                    id="carbon-VideoPlatformEditor-video-id"
                    value={searchVideoId ?? ''}
                    onChange={setSearchVideoId}
                    placeholder="Paste link to video"
                    onEnterKey={() => searchVideo(searchVideoId)}
                />
                <IconButton style="primary" disabled={!searchVideoId} icon="search" onClick={() => searchVideo(searchVideoId)} />
            </div>
        }

        return <>
            <div className={style.buttonRow}>
                <a href={props.value.uri} target="_blank">
                    <Button title="Open video">
                        <Icon icon="play" />
                        &nbsp;&nbsp;Video
                    </Button>
                </a>

                <div>
                    <IconButton icon="sync" title="Update metadata" onClick={() => searchVideo(props.value.uri)} />
                    <IconButton icon="xmark" title="Remove video" onClick={() => props.commit("")} />
                </div>
            </div>

            <Label htmlFor="carbon-VideoPlatformEditor-thumbnail">{translate('Carbon.VideoPlatformEditor:Main:inspector.thumbnail', 'Video Thumbnail')}</Label>
            <ImageEditor
                id="carbon-VideoPlatformEditor-thumbnail"
                options={{
                    features: {
                        crop: false
                    }
                }}
                value={imageObject}
                commit={setImage}
                renderSecondaryInspector={props.renderSecondaryInspector}
            />

            <MetadataView video={props.value} />
        </>
    }
}
