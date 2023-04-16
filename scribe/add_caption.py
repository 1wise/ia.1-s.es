import sys
from PIL import Image, PngImagePlugin
from textwrap import wrap

def wrap_text(text, max_width=80):
    return '\n'.join(wrap(text, width=max_width))

def add_caption_to_png_metadata(input_file, output_file, caption):
    # Wrap the caption to ensure lines are no longer than 80 characters
    wrapped_caption = wrap_text(caption)

    # Open the image with PIL
    img = Image.open(input_file)

    # Set the caption as metadata
    metadata = PngImagePlugin.PngInfo()
    metadata.add_text("Caption", wrapped_caption)

    # Save the image with the new metadata
    img.save(output_file, "PNG", pnginfo=metadata)

if __name__ == "__main__":
    input_file = sys.argv[1]
    output_file = sys.argv[2]
    caption = sys.argv[3]

    add_caption_to_png_metadata(input_file, output_file, caption)

