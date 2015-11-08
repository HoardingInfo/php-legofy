$(document).ready(function() {

    var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
    var canvas = $("canvas")[0];
    var video = $('video')[0];

    if (!is_chrome) {
        $('.compatible').hide();
        $('.not-compatible').show();
        return;
    }

    var ctx = canvas.getContext("2d");

    video.addEventListener('play', function(){
        $(canvas).show();
        var cw = Math.floor(video.clientWidth);
        var ch = Math.floor(video.clientHeight);
        legofy(this, ctx, cw, ch);
    },false);

    function legofy(img, ctx, width, height) {
        if(img.paused || img.ended) return false;
        canvas.width = width;
        canvas.height = height;
        ctx.mozImageSmoothingEnabled = false;
        ctx.imageSmoothingEnabled = false;
        var size = 0.05;
        var w = width * size;
        var h = height * size;
        ctx.drawImage(img, 0, 0, w, h);
        ctx.rect(0, 0, width, height);
        ctx.drawImage(canvas, 0, 0, w, h, 0, 0, width, height);
        ctx.globalCompositeOperation = "overlay";
        ctx.rect(0, 0, 800, 800);
        ctx.fillStyle = pat;
        ctx.fill();
        setTimeout(legofy, 20, img, ctx, width, height);
    }

    var brick = new Image();
    brick.src = "data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAABkAAD/4QMraHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjMtYzAxMSA2Ni4xNDU2NjEsIDIwMTIvMDIvMDYtMTQ6NTY6MjcgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDUzYgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjE5MkJCOEFGODBCMDExRTU5RDc2QkJFNUU3MDAwRTNDIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjE5MkJCOEIwODBCMDExRTU5RDc2QkJFNUU3MDAwRTNDIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MTkyQkI4QUQ4MEIwMTFFNTlENzZCQkU1RTcwMDBFM0MiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MTkyQkI4QUU4MEIwMTFFNTlENzZCQkU1RTcwMDBFM0MiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7/7gAOQWRvYmUAZMAAAAAB/9sAhAABAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAgICAgICAgICAgIDAwMDAwMDAwMDAQEBAQEBAQIBAQICAgECAgMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwP/wAARCAAUABQDAREAAhEBAxEB/8QAbAAAAgMBAAAAAAAAAAAAAAAABAYFBwgJAQEAAAAAAAAAAAAAAAAAAAAAEAAABAUDAwEFCQAAAAAAAAACAwQFARESFAYTFRYAIRciMWEjJQcyYpJDgyQ0CBgRAQAAAAAAAAAAAAAAAAAAAAD/2gAMAwEAAhEDEQA/ALJCNpTHrz3g1YJIgLCaJubjE5S5ebErWEUJcqAcW2oyEwYnKVFBx9MoFgEIU4AQWpxZ5Ng2kY8rxRxU0FtrwjycvJGk1WamisSpH1CtjBSUmWEB/lpBwGQOPqLFGEQQBR1Q7be6no1JV6vorubej2aWpcfCqlV75dBWK/KEpH1Ef8CdzqHZ/ARkOBlnrUzYF/OLQp292akKhUERahe129UUxUbkwk/UAEQQDh0EtkKtrxJudczzBAlx7FsdEoeXJ5dhtCJQmjclKk6IshI3pQrFLmoLAnLb0sZmuWmanDAERw6BH3fIvCHKLFbv11zfY6xXWlvvIdnplO5230USlrdugA/sz4O4Yf5gvrLcCNi2u75byKkNrxbbf328akpaHar2dugxt9PvCPOMY8sf6OuL0jx3565DwzdJRsbW8+WbzKWjcfFn9nv0HTn5RtP59rr/AHtKqr8dUv0qPf0H/9k=";
    var pat = ctx.createPattern(brick, "repeat");
});