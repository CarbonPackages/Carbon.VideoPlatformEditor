# Carbon VideoPlatformEditor

A Neos CMS plugin that provides editors and infrastructure for managing videos from popular platforms (YouTube and Vimeo), with automatic metadata extraction and thumbnail downloading.

## Features

- **Multi-platform support**: Seamless integration with YouTube and Vimeo
- **Inspector Editor**: User-friendly UI component for selecting and managing videos in Neos backend
- **Automatic Metadata Extraction**: Fetches video metadata including title, duration, and aspect ratio
- **Thumbnail Management**: Automatically downloads and stores video thumbnails as Neos assets
- **oEmbed Integration**: Uses oEmbed protocol for reliable metadata extraction
- **TypeScript Support**: Modern frontend development with TypeScript and React

## Requirements

- Neos/Neos-UI ~9.1.4

### Requirements for for building the plugin

- Node.js 24
- Yarn 3.2.0

## Installation

```bash
composer require carbon/videoplatformeditor
```

## Project Structure

### Backend (PHP)

**Core Classes:**

- `Video` - Represents a video entity with metadata
- `VideoPlatformType` - Enum for platform types (YouTube, Vimeo)
- `YoutubeVideoId` / `VimeoVideoId` - Platform-specific video ID models
- `AspectRatio` - Video aspect ratio handling
- `AssetId` - Reference to Neos asset resources

**Infrastructure:**

- `OembedMetadataProvider` - Retrieves metadata via oEmbed protocol
- `YoutubeContentDetailsProvider` - YouTube-specific metadata extraction
- `ImageImporter` - Handles thumbnail asset creation
- `OembedHtmlExtractor` - Parses oEmbed HTML responses
- `AssetUsageExtractionAspect` - Tracks asset usage relationships

**Controllers:**

- `VideoPlatformController` - Backend API endpoints
- `VideoQuery` - Query builder for video searches

### Frontend (TypeScript/React)

**Core Module** (`Modules/core/`):

- `InspectorEditor` - React component for the Neos inspector UI
- `MetadataView` - Displays video metadata and preview
- `Video` domain model - TypeScript representation of video data
- HTTP client for backend communication

**Plugin Module** (`Modules/plugin/`):

- Built with esbuild for production optimization
- Compiled JavaScript and CSS served from `Resources/Public/`

## Configuration

### Settings.Neos.Ui.yaml

Registers the VideoPlatformEditor as the inspector editor for the `Video` data type:

```yaml
Neos:
  Neos:
    userInterface:
      inspector:
        dataTypes:
          Carbon\VideoPlatformEditor\Video:
            editor: Carbon.VideoPlatformEditor/Inspector/Editors/VideoPlatformEditor
```

### Settings.Mvc.yaml

Contains MVC-specific configurations for the backend controllers.

### Policy.yaml

Defines access control policies for video platform operations.

## Usage

### PHP Integration

```php
use Carbon\VideoPlatformEditor\Video;
use Carbon\VideoPlatformEditor\YoutubeVideoId;

$video = new Video(
    id: YoutubeVideoId::fromString('dQw4w9WgXcQ'),
    title: 'Video Title',
    duration: 212,
    aspectRatio: AspectRatio::SIXTEEN_NINE,
    thumbnail: null
);
```

### Neos NodeType

Define a video property in your NodeType:

```yaml
'MyPackage:Content.Video':
  superTypes:
    'Neos.Neos:Content': true
  properties:
    video:
      type: 'Carbon\VideoPlatformEditor\Video'
```

## Development

### Build Commands

```bash
# Type checking
yarn lint

# Build plugin
yarn build
```

### Project Workspaces

This project uses Yarn workspaces:

- `Modules/core` - Core TypeScript domain and utilities
- `Modules/plugin` - Neos UI plugin build

## Testing

Unit and functional tests are located in `Tests/`:

```bash
# Run tests
vendor/bin/phpunit Tests/
```

Test coverage includes:

- Video serialization/deserialization
- oEmbed metadata extraction
- YouTube content details
- Aspect ratio handling
- Asset usage tracking

## License

GPL-3.0-or-later

## Support

For issues and feature requests, please refer to the repository's issue tracker.
