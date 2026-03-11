
export type IVideo = ({
    platformType: "YOUTUBE"
    id: {
        videoId: string,
        videoType: string,
    }
} | {
    platformType: "VIMEO"
    id: {
        videoId: string,
        hash: string,
    }
}) & {
    title: string
    thumbnail: {
        id: string
    },
    aspectRatio: `${number} / ${number}`
    uri: string
}
